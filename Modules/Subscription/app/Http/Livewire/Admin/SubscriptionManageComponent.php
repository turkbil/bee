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
    public $user_id;
    public $subscription_plan_id;
    public $cycle_key;
    public $status = 'active';
    public $has_trial = false;
    public $trial_days = 0;
    public $auto_renew = true;
    public $started_at;
    public $current_period_end;

    // Computed
    public $price_per_cycle = 0;
    public $currency = 'TRY';
    public $available_cycles = [];
    public $selected_cycle_metadata = [];

    public function boot()
    {
        view()->share('pretitle', $this->subscriptionId ? 'Abonelik Düzenle' : 'Yeni Abonelik');
        view()->share('title', $this->subscriptionId ? 'Abonelik Düzenle' : 'Yeni Abonelik');
    }

    public function mount($id = null)
    {
        $this->boot();
        $this->started_at = now()->format('Y-m-d\TH:i');
        $this->current_period_end = now()->addMonth()->format('Y-m-d\TH:i');

        if ($id) {
            $this->subscriptionId = (int) $id;
            $this->loadSubscription();
        }
    }

    protected function loadSubscription()
    {
        $subscription = Subscription::findOrFail($this->subscriptionId);

        $this->user_id = $subscription->user_id;
        $this->subscription_plan_id = $subscription->subscription_plan_id;
        $this->cycle_key = $subscription->cycle_key;
        $this->status = $subscription->status;
        $this->has_trial = (bool) $subscription->has_trial;
        $this->trial_days = (int) $subscription->trial_days;
        $this->auto_renew = (bool) $subscription->auto_renew;
        $this->price_per_cycle = (float) $subscription->price_per_cycle;
        $this->started_at = $subscription->started_at?->format('Y-m-d\TH:i');
        $this->current_period_end = $subscription->current_period_end?->format('Y-m-d\TH:i');

        // Load plan cycles
        if ($this->subscription_plan_id) {
            $this->loadPlanCycles();
        }
    }

    protected function loadPlanCycles()
    {
        $plan = SubscriptionPlan::find($this->subscription_plan_id);
        if ($plan) {
            $this->available_cycles = $plan->getSortedCycles();
        }
    }

    public function updatedSubscriptionPlanId()
    {
        if ($this->subscription_plan_id) {
            $plan = SubscriptionPlan::find($this->subscription_plan_id);

            if ($plan) {
                // Load plan cycles
                $this->loadPlanCycles();

                // Set currency from plan
                $this->currency = $plan->currency ?? 'TRY';

                // Reset cycle selection
                $this->cycle_key = null;
                $this->price_per_cycle = 0;
                $this->selected_cycle_metadata = [];

                // Auto-select first cycle if available
                if (!empty($this->available_cycles)) {
                    $firstCycleKey = array_key_first($this->available_cycles);
                    $this->cycle_key = $firstCycleKey;
                    $this->updatedCycleKey();
                }

                // If plan is trial, auto-enable trial
                if ($plan->is_trial && !$this->subscriptionId) {
                    $this->has_trial = true;
                }
            }
        }
    }

    public function updatedCycleKey()
    {
        if ($this->cycle_key && !empty($this->available_cycles[$this->cycle_key])) {
            $cycle = $this->available_cycles[$this->cycle_key];

            // Store full cycle metadata
            $this->selected_cycle_metadata = $cycle;

            // Update price
            $this->price_per_cycle = (float) ($cycle['price'] ?? 0);

            // Update trial days from cycle (if not manually set and new subscription)
            if (!$this->subscriptionId && isset($cycle['trial_days']) && $cycle['trial_days'] > 0) {
                $this->has_trial = true;
                $this->trial_days = (int) $cycle['trial_days'];
            }

            // Update period end based on duration
            $this->updatePeriodEnd();
        }
    }

    public function updatedStartedAt()
    {
        $this->updatePeriodEnd();
    }

    protected function updatePeriodEnd()
    {
        if ($this->started_at && $this->cycle_key && !empty($this->available_cycles[$this->cycle_key])) {
            $cycle = $this->available_cycles[$this->cycle_key];
            $durationDays = (int) ($cycle['duration_days'] ?? 30);

            $start = \Carbon\Carbon::parse($this->started_at);
            $this->current_period_end = $start->addDays($durationDays)->format('Y-m-d\TH:i');
        }
    }

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,subscription_plan_id',
            'cycle_key' => 'required|string',
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

        $plan = SubscriptionPlan::findOrFail($this->subscription_plan_id);
        $cycle = $plan->getCycle($this->cycle_key);

        if (!$cycle) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Geçersiz süre seçimi!',
                'type' => 'error'
            ]);
            return;
        }

        $data = [
            'user_id' => $this->user_id,
            'subscription_plan_id' => $plan->subscription_plan_id,
            'status' => $this->status,
            'cycle_key' => $this->cycle_key,
            'cycle_metadata' => $this->selected_cycle_metadata ?: [
                'label' => $cycle['label'] ?? [],
                'duration_days' => $cycle['duration_days'] ?? 30,
                'price' => $cycle['price'] ?? 0,
                'compare_price' => $cycle['compare_price'] ?? null,
                'trial_days' => $cycle['trial_days'] ?? null,
                'badge' => $cycle['badge'] ?? null,
                'promo_text' => $cycle['promo_text'] ?? null,
                'sort_order' => $cycle['sort_order'] ?? 0,
            ],
            'price_per_cycle' => $this->price_per_cycle,
            'currency' => $this->currency,
            'has_trial' => $this->has_trial,
            'trial_days' => $this->has_trial ? $this->trial_days : 0,
            'trial_ends_at' => $this->has_trial ? now()->addDays($this->trial_days) : null,
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

                // If trial subscription, mark user as has_used_trial
                if ($this->has_trial) {
                    User::where('id', $this->user_id)
                        ->update(['has_used_trial' => true]);
                }
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
