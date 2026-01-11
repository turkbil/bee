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
        'currency' => 'TRY',
        'tax_rate' => 20.00,
        'price_display_mode' => 'show',
        'device_limit' => 1,
        'is_trial' => false,
        'is_featured' => false,
        'is_active' => true,
    ];

    // Features array
    public $features = [];

    // Billing Cycles
    public $cycles = [];

    // Universal Component Data
    public $currentLanguage;
    public $availableLanguages = [];

    public function boot()
    {
        view()->share('pretitle', $this->planId ? __('subscription::admin.edit_plan') : __('subscription::admin.new_plan'));
        view()->share('title', $this->planId ? __('subscription::admin.edit_plan') : __('subscription::admin.new_plan'));
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
        $this->cycles = [];
    }

    protected function loadPlanData()
    {
        $plan = SubscriptionPlan::findOrFail($this->planId);

        // Dil-neutral alanlar
        $this->inputs = [
            'slug' => $plan->slug ?? '',
            'currency' => $plan->currency ?? 'TRY',
            'tax_rate' => (float) ($plan->tax_rate ?? 20.00),
            'price_display_mode' => $plan->price_display_mode ?? 'show',
            'device_limit' => (int) ($plan->device_limit ?? 1),
            'is_trial' => (bool) $plan->is_trial,
            'is_featured' => (bool) $plan->is_featured,
            'is_active' => (bool) $plan->is_active,
        ];

        // Features
        $this->features = is_array($plan->features) ? $plan->features : [];

        // Billing Cycles
        $this->cycles = is_array($plan->billing_cycles) ? $plan->billing_cycles : [];

        // Çoklu dil alanları
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => $plan->getTranslated('title', $lang) ?? '',
                'description' => $plan->getTranslated('description', $lang) ?? '',
            ];
        }
    }

    /**
     * Title değiştiğinde slug'ı otomatik oluştur
     */
    public function updated($propertyName)
    {
        // Ana dil title'ı değiştiğinde slug'ı otomatik oluştur (sadece yeni kayıtta)
        $defaultLang = $this->availableLanguages[0] ?? 'tr';

        if ($propertyName === "multiLangInputs.{$defaultLang}.title" && !$this->planId) {
            $title = $this->multiLangInputs[$defaultLang]['title'] ?? '';
            if (!empty($title)) {
                $this->inputs['slug'] = \Str::slug($title);
            }
        }
    }

    protected function rules()
    {
        return [
            'inputs.slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $this->planId . ',subscription_plan_id',
            'inputs.currency' => 'required|string|in:TRY,USD,EUR',
            'inputs.tax_rate' => 'required|numeric|min:0|max:100',
            'inputs.price_display_mode' => 'required|string|in:show,hide,request',
            'inputs.device_limit' => 'required|integer|min:1',
            'inputs.is_featured' => 'boolean',
            'inputs.is_active' => 'boolean',
            'multiLangInputs.*.title' => 'nullable|string|max:255',
            'multiLangInputs.*.description' => 'nullable|string',
        ];
    }

    /**
     * Yeni cycle ekle
     */
    public function addCycle($cycleData)
    {
        // Deneme üyeliğiyse sadece 1 cycle'a izin ver
        if ($this->inputs['is_trial'] && count($this->cycles) >= 1) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('subscription::admin.trial_single_cycle_only'),
                'type' => 'error'
            ]);
            return;
        }

        $cycleKey = \Str::slug($cycleData['label_tr'] ?? 'cycle');

        $this->cycles[$cycleKey] = [
            'label' => [
                'tr' => $cycleData['label_tr'] ?? '',
                'en' => $cycleData['label_en'] ?? $cycleData['label_tr'] ?? '',
            ],
            'price' => (float) ($cycleData['price'] ?? 0),
            'price_type' => $cycleData['price_type'] ?? 'without_tax', // KDV Hariç/Dahil
            'compare_price' => !empty($cycleData['compare_price']) ? (float) $cycleData['compare_price'] : null,
            'duration_days' => (int) ($cycleData['duration_days'] ?? 30),
            'trial_days' => !empty($cycleData['trial_days']) ? (int) $cycleData['trial_days'] : null,
            'badge' => [
                'text' => $cycleData['badge_text'] ?? null,
                'color' => $cycleData['badge_color'] ?? null,
            ],
            'promo_text' => [
                'tr' => $cycleData['promo_text_tr'] ?? null,
                'en' => $cycleData['promo_text_en'] ?? null,
            ],
            'sort_order' => (int) ($cycleData['sort_order'] ?? count($this->cycles) + 1),
        ];

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('subscription::admin.cycle_added'),
            'type' => 'success'
        ]);
    }

    /**
     * Cycle güncelle
     */
    public function updateCycle($cycleKey, $cycleData)
    {
        if (!isset($this->cycles[$cycleKey])) {
            return;
        }

        $this->cycles[$cycleKey] = [
            'label' => [
                'tr' => $cycleData['label_tr'] ?? '',
                'en' => $cycleData['label_en'] ?? $cycleData['label_tr'] ?? '',
            ],
            'price' => (float) ($cycleData['price'] ?? 0),
            'price_type' => $cycleData['price_type'] ?? 'without_tax', // KDV Hariç/Dahil
            'compare_price' => !empty($cycleData['compare_price']) ? (float) $cycleData['compare_price'] : null,
            'duration_days' => (int) ($cycleData['duration_days'] ?? 30),
            'trial_days' => !empty($cycleData['trial_days']) ? (int) $cycleData['trial_days'] : null,
            'badge' => [
                'text' => $cycleData['badge_text'] ?? null,
                'color' => $cycleData['badge_color'] ?? null,
            ],
            'promo_text' => [
                'tr' => $cycleData['promo_text_tr'] ?? null,
                'en' => $cycleData['promo_text_en'] ?? null,
            ],
            'sort_order' => (int) ($cycleData['sort_order'] ?? $this->cycles[$cycleKey]['sort_order'] ?? 999),
        ];

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('subscription::admin.cycle_updated'),
            'type' => 'success'
        ]);
    }

    /**
     * Cycle sil
     */
    public function removeCycle($cycleKey)
    {
        unset($this->cycles[$cycleKey]);

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('subscription::admin.cycle_deleted'),
            'type' => 'success'
        ]);
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
            $this->addError('multiLangInputs', __('subscription::admin.title_required'));
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
            'currency' => $this->inputs['currency'],
            'tax_rate' => $this->inputs['tax_rate'],
            'price_display_mode' => $this->inputs['price_display_mode'],
            'billing_cycles' => $this->cycles,
            'device_limit' => $this->inputs['device_limit'],
            'features' => $featuresArray,
            'is_trial' => $this->inputs['is_trial'],
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
