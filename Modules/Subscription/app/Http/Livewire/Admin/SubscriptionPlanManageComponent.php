<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\LanguageManagement\App\Models\TenantLanguage;

#[Layout('admin.layout')]
class SubscriptionPlanManageComponent extends Component
{
    public $planId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'slug' => '',
        'price_monthly' => 0,
        'price_yearly' => 0,
        'compare_price_monthly' => null,
        'compare_price_yearly' => null,
        'trial_days' => 0,
        'device_limit' => 1,
        'is_featured' => false,
        'is_active' => true,
    ];

    // Features array
    public $features = [];
    public $newFeature = '';

    // Universal Component Data
    public $currentLanguage;
    public $availableLanguages = [];

    public function boot()
    {
        view()->share('pretitle', $this->planId ? __('subscription::admin.edit_plan') : __('subscription::admin.new_plan'));
    }

    public function mount($id = null)
    {
        $this->boot();
        $this->initializeLanguages();

        if ($id) {
            $this->planId = (int) $id;
            $this->loadPlanData();
        } else {
            $this->initializeEmptyInputs();
        }
    }

    protected function initializeLanguages()
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        $this->currentLanguage = $this->availableLanguages[0] ?? 'tr';
    }

    protected function initializeEmptyInputs()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'description' => '',
            ];
        }

        $this->features = [];
    }

    protected function loadPlanData()
    {
        $plan = SubscriptionPlan::findOrFail($this->planId);

        // Dil-neutral alanlar
        $this->inputs = [
            'slug' => $plan->slug ?? '',
            'price_monthly' => (float) $plan->price_monthly,
            'price_yearly' => (float) $plan->price_yearly,
            'compare_price_monthly' => $plan->compare_price_monthly ? (float) $plan->compare_price_monthly : null,
            'compare_price_yearly' => $plan->compare_price_yearly ? (float) $plan->compare_price_yearly : null,
            'trial_days' => (int) ($plan->trial_days ?? 0),
            'device_limit' => (int) ($plan->device_limit ?? 1),
            'is_featured' => (bool) $plan->is_featured,
            'is_active' => (bool) $plan->is_active,
        ];

        // Features
        $this->features = is_array($plan->features) ? $plan->features : [];

        // Çoklu dil alanları
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => $plan->getTranslated('title', $lang) ?? '',
                'description' => $plan->getTranslated('description', $lang) ?? '',
            ];
        }
    }

    protected function rules()
    {
        return [
            'inputs.slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $this->planId . ',subscription_plan_id',
            'inputs.price_monthly' => 'required|numeric|min:0',
            'inputs.price_yearly' => 'required|numeric|min:0',
            'inputs.compare_price_monthly' => 'nullable|numeric|min:0',
            'inputs.compare_price_yearly' => 'nullable|numeric|min:0',
            'inputs.trial_days' => 'nullable|integer|min:0',
            'inputs.device_limit' => 'required|integer|min:1',
            'inputs.is_featured' => 'boolean',
            'inputs.is_active' => 'boolean',
            'multiLangInputs.*.title' => 'nullable|string|max:255',
            'multiLangInputs.*.description' => 'nullable|string',
        ];
    }

    public function addFeature()
    {
        if (!empty(trim($this->newFeature))) {
            $this->features[] = trim($this->newFeature);
            $this->newFeature = '';
        }
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function save()
    {
        $this->validate();

        // Check if at least one language has title
        $hasTitle = false;
        foreach ($this->multiLangInputs as $data) {
            if (!empty($data['title'])) {
                $hasTitle = true;
                break;
            }
        }

        if (!$hasTitle) {
            $this->addError('multiLangInputs', __('subscription::admin.plans.title_required'));
            return;
        }

        // Prepare multi-language data
        $titleArray = [];
        $descriptionArray = [];

        foreach ($this->multiLangInputs as $lang => $data) {
            if (!empty($data['title'])) {
                $titleArray[$lang] = $data['title'];
            }
            if (!empty($data['description'])) {
                $descriptionArray[$lang] = $data['description'];
            }
        }

        // Prepare features (clean array of strings)
        $featuresArray = [];
        foreach ($this->features as $feature) {
            if (is_string($feature) && !empty(trim($feature))) {
                $featuresArray[] = trim($feature);
            }
        }

        $data = [
            'title' => $titleArray,
            'description' => $descriptionArray,
            'slug' => $this->inputs['slug'],
            'price_monthly' => $this->inputs['price_monthly'],
            'price_yearly' => $this->inputs['price_yearly'],
            'compare_price_monthly' => $this->inputs['compare_price_monthly'] ?: null,
            'compare_price_yearly' => $this->inputs['compare_price_yearly'] ?: null,
            'trial_days' => $this->inputs['trial_days'] ?: 0,
            'device_limit' => $this->inputs['device_limit'],
            'features' => $featuresArray,
            'is_featured' => $this->inputs['is_featured'],
            'is_active' => $this->inputs['is_active'],
        ];

        try {
            if ($this->planId) {
                $plan = SubscriptionPlan::findOrFail($this->planId);
                $plan->update($data);
                $message = __('admin.updated_successfully');
            } else {
                $data['sort_order'] = (SubscriptionPlan::max('sort_order') ?? 0) + 1;
                SubscriptionPlan::create($data);
                $message = __('admin.created_successfully');
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success'
            ]);

            return redirect()->route('admin.subscription.plans.index');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed') . ': ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('subscription::admin.livewire.subscription-plan-manage-component');
    }
}
