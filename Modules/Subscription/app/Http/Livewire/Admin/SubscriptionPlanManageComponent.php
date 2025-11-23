<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Modules\Subscription\App\Models\SubscriptionPlan;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class SubscriptionPlanManageComponent extends Component
{
    public $planId = null;
    public $currentLanguage = 'tr';
    public $availableLanguages = [];
    public $multiLangInputs = [];

    // Form fields
    public $slug = '';
    public $price_monthly = 0;
    public $price_yearly = 0;
    public $compare_price_monthly = null;
    public $compare_price_yearly = null;
    public $trial_days = 0;
    public $device_limit = 1;
    public $features = [];
    public $is_featured = false;
    public $is_active = true;

    // Feature input
    public $newFeature = '';

    protected $listeners = [
        'switchLanguage' => 'switchLanguage',
    ];

    protected function rules()
    {
        return [
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $this->planId,
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'device_limit' => 'required|integer|min:1',
        ];
    }

    public function mount($id = null)
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();

        $this->currentLanguage = $this->availableLanguages[0] ?? 'tr';

        $this->initializeFormData();

        if ($id) {
            $this->planId = $id;
            $this->loadPlan();
        }
    }

    protected function initializeFormData()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'description' => '',
            ];
        }
    }

    protected function loadPlan()
    {
        $plan = SubscriptionPlan::findOrFail($this->planId);

        $this->slug = $plan->slug;
        $this->price_monthly = $plan->price_monthly;
        $this->price_yearly = $plan->price_yearly;
        $this->compare_price_monthly = $plan->compare_price_monthly;
        $this->compare_price_yearly = $plan->compare_price_yearly;
        $this->trial_days = $plan->trial_days;
        $this->device_limit = $plan->device_limit ?? 1;
        $this->features = $plan->features ?? [];
        $this->is_featured = $plan->is_featured;
        $this->is_active = $plan->is_active;

        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang]['title'] = $plan->getTranslated('title', $lang) ?? '';
            $this->multiLangInputs[$lang]['description'] = $plan->getTranslated('description', $lang) ?? '';
        }
    }

    public function switchLanguage($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
        }
    }

    public function generateSlug()
    {
        $title = $this->multiLangInputs[$this->currentLanguage]['title'] ?? '';
        if (!empty($title)) {
            $this->slug = Str::slug($title);
        }
    }

    public function addFeature()
    {
        if (!empty($this->newFeature)) {
            $this->features[] = $this->newFeature;
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

        // Validate at least one language has title
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

        try {
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

            $data = [
                'title' => $titleArray,
                'description' => $descriptionArray,
                'slug' => $this->slug,
                'price_monthly' => $this->price_monthly,
                'price_yearly' => $this->price_yearly,
                'compare_price_monthly' => $this->compare_price_monthly,
                'compare_price_yearly' => $this->compare_price_yearly,
                'trial_days' => $this->trial_days,
                'device_limit' => $this->device_limit,
                'features' => $this->features,
                'is_featured' => $this->is_featured,
                'is_active' => $this->is_active,
            ];

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

    #[Computed]
    public function availableLanguagesList()
    {
        return TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function render()
    {
        return view('subscription::admin.livewire.subscription-plan-manage-component');
    }
}
